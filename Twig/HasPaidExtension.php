<?php

namespace Vidarl\DummyBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use eZ\Publish\API\Repository\Repository;
use Vidarl\DummyBundle\ContextProvider\PaidSubscriber;


/**
 * Class HasPaidExtension
 *
 * Has one twig function hasPaid(). Usage :
 * {% if hasPaid() %}
 *     Congrats, you have paid<br/>
 * {%  else %}
 *     No money<br/>
 * {%  endif %}
 *
 * Make sure your controller has `Vary: X-User-Hash` ( default setting for default view controller), or this will not be cached correctly in http cache
 *
 * @package Vidarl\DummyBundle\Twig
 */
class HasPaidExtension extends AbstractExtension
{
    /**
     * @var \eZ\Publish\Core\Repository\Repository
     */
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('hasPaid', [$this, 'hasPaid']),
        ];
    }

    public function hasPaid()
    {
        $user = $this->repository->getCurrentUser();
        $hasPaid = PaidSubscriber::hasPaid($user);

        return $hasPaid;
    }

}
