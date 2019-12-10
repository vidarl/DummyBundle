<?php

namespace Vidarl\DummyBundle\ContextProvider;

use FOS\HttpCache\UserContext\ContextProviderInterface;
use FOS\HttpCache\UserContext\UserContext;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\User\User;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class PaidSubscriber
 *
 * Basically, we want the info about if user has payed or not to be part of user_context_hash ( which is eZ terms is a role hash, not user hash btw)
 * So, to enrich the user_context_hash, see https://foshttpcachebundle.readthedocs.io/en/1.3/reference/configuration/user-context.html#custom-context-providers
 *
 * This class sets the user_context_hash accordingly.
 * You may check in twig if user has payed, ref. Vidarl\DummyBundle\Twig\HasPaidExtension class but it is recommended to use the
 * `hasPaid` variable which is already set by the custom view controller PaywallController::articlePaywallViewAction
 *
 * There is scenario for cache inconsistency here:
 * - nothing is cached
 * - A user who as payed, enters sites:
 *   - user_context_hash is fetched and cached in http cache
 *   - user loads pages behind paywall ( hasPaid=true ), these are also cached now
 * - User subscription ends, so hasPaid=false.
 *   - but user_context_hash is still cached in http cache for some time....
 *   User access page which is in cache :
 *     - he will see content behind paywall
 *   User access page which is not in cache :
 *     - he will not see content behind paywall, but it will be cached as behind paywall (because user_context_hash is wrong for user ) - BAD!
 *
 * This is the reason why we also need a custom view controller, which will tell the http cache not to cache the response if user_context_hash is obsolete.
 * all user_context_hashes should be purged once someone subscribe/unsubscribe to avoid pages from being uncached.
 * This is done by clearing the xkey `ez-user-context-hash`
 * CLI : php bin/console fos:httpcache:invalidate:tag ez-user-context-hash
 *
 *
 *
 * @package Vidarl\DummyBundle\ContextProvider
 */

class PaidSubscriber implements ContextProviderInterface
{
    /**
     * @var \eZ\Publish\Core\Repository\Repository
     */
    protected $repository;

    /**
     * @var bool|null
     */
    protected $hasPaid = null;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Prototype, this should be in a service instead
     * Function also used by twig function hasPaid() and PaywallController::articlePaywallViewAction()
     *
     * @param User $user
     * @return bool
     */
    static public function hasPaid(User $user)
    {
        $login = $user->login;

        //Put in your own logic for this...
        //$hasPaid = $login !== 'anonymous';
        $hasPaid = $login === 'vidarpaying';
        if ($login === 'vidarnotpaying') {
            $hasPaid = true;
            $hasPaid = false;
        }

        //debug stuff:
        //file_put_contents('/tmp/payedsubscriber.log', "has Paid:$hasPaid, $login\n", FILE_APPEND);

        return $hasPaid;
    }

    public function updateUserContext(UserContext $context)
    {
        $user = $this->repository->getCurrentUser();
        $this->hasPaid = self::hasPaid($user);

        $context->addParameter('hasPaid', $this->hasPaid);
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $requestUri = $event->getRequest()->getRequestUri();
        //Let's only decorate user context hash requests
        if ($requestUri !== '/_fos_user_context_hash') {
            return;
        }
        $responseHeaders = $event->getResponse()->headers;

        // We tell the http proxy if the response is for a user behind paywall or not.
        // This information is then passed on from the http proxy to the controller (which will validate it)
        if ($this->hasPaid === true) {
            // This is a user-context request, and user has access behind paywall
            $responseHeaders->set('x-httpcache-haspaid', 'true');
            //file_put_contents('/tmp/payedsubscriber.log', "x-httpcache-haspaid: true\n", FILE_APPEND);
        } elseif ($this->hasPaid === false) {
            // This is a user-context request, and but user has  not access behind paywall
            $responseHeaders->set('x-httpcache-haspaid', 'false');
            //file_put_contents('/tmp/payedsubscriber.log', "x-httpcache-haspaid: false\n", FILE_APPEND);
        } else {
            // We should not ever get here...
            //file_put_contents('/tmp/payedsubscriber.log', "header x-httpcache-haspaid not set\n", FILE_APPEND);
        }
    }
}
