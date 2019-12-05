<?php

namespace Vidarl\DummyBundle\ContextProvider;

use FOS\HttpCache\UserContext\ContextProviderInterface;
use FOS\HttpCache\UserContext\UserContext;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\User\User;

/**
 * Class PaidSubscriber
 *
 * Basically, we want the info about if user has payed or not to be part of user_context_hash ( which is eZ terms is a role hash, not user hash btw)
 * So, to enrich the user_context_hash, see https://foshttpcachebundle.readthedocs.io/en/1.3/reference/configuration/user-context.html#custom-context-providers
 *
 * This class sets the user_context_hash accordingly.
 * You may check in twig if user has payed, ref. Vidarl\DummyBundle\Twig\HasPaidExtension class
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
 * Solution to this is to clear cache for all user_context_hashes when someone subscribe/unsubscribe
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

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Prototype, this should be in a service instead
     * Function also used by twig function hasPaid()
     *
     * @param User $user
     * @return bool
     */
    static public function hasPaid(User $user)
    {
        $login = $user->login;

        //Put in your own logic for this...
        $hasPaid = $login !== 'anonymous';

        //debug stuff:
        //file_put_contents('/tmp/payedsubscriber.log', "has Paid:$hasPaid, $login\n", FILE_APPEND);

        return $hasPaid;
    }

    public function updateUserContext(UserContext $context)
    {
        $user = $this->repository->getCurrentUser();
        $hasPaid = self::hasPaid($user);

        $context->addParameter('hasPaid', $hasPaid);
    }
}
