<?php


namespace Vidarl\DummyBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Symfony\Component\HttpFoundation\Request;
use Vidarl\DummyBundle\ContextProvider\PaidSubscriber;

/**
 * Class PaywallController
 *
 * The purpose of this view controller is to prevent http cache to be polluted when subscriptions expires. There
 * is a possibility for this if user_context_hashes are not purged in http cache immediately after subscription has expired.
 * See PaidSubscriber for more info
 *
 * In order for this workaround to work, custom .vcl is needed by varnish/Fastly.
 * see
 *
 * Template code example ( full view template ):
 * {% if hasPaid %}
 *   Congrats, you have paid<br/>
 * {% else %}
 *   No money<br/>
 * {% endif %}
 *
 * @package Vidarl\DummyBundle\Controller
 */
class PaywallController extends Controller
{
    public function articlePaywallViewAction(ContentView $view, Request $request)
    {
        $userId = $this->getRepository()->getPermissionResolver()->getCurrentUserReference()->getUserId();
        $user = $this->getRepository()->getUserService()->loadUser($userId);
        $hasPaid = PaidSubscriber::hasPaid($user);

        // We'll export to the twig template if there is a mismatch : http cache thinks user has access to paywall, but in reality he does not ( user access expired, but his old user context is still in http cache)
        $httpCacheContextMismatch = false;

        // We'll export to the twig template if user is behind paywall or not
        $view->addParameters(['hasPaid' => $hasPaid]);

        $xHttpCacheHasPaid = $request->headers->get('x-httpcache-haspaid');
        // If http cache believes user is behind paywall but he is not, or vise versa we tell http cache not to cache response
        if ( ($xHttpCacheHasPaid === 'true' && !$hasPaid) || ($xHttpCacheHasPaid === 'false' && $hasPaid) )
        {
            $view->setCacheEnabled(false);
            $httpCacheContextMismatch = true;
        }
        $view->addParameters(['httpCacheContextMismatch' => $httpCacheContextMismatch]);

        return $view;
    }
}
