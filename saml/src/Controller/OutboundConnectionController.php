<?php

namespace Drupal\saml\Controller;

use LightSaml\SamlConstants;
use LightSaml\Binding\BindingFactory;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\saml\Entity\ServiceProviderMock;
use LightSaml\Context\Profile\MessageContext;
use Drupal\saml\Entity\ServiceProviderInterface;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\saml\Factory\Model\Protocol\SamlMessageFactoryInterface;

/**
 * Provides a controller for identity providers.
 */
class OutboundConnectionController extends ControllerBase {

  /**
   * Current user account.
   *
   * @var Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * Saml message factory.
   *
   * @var Drupal\saml\Factory\Model\Protocol\SamlMessageFactoryInterface
   */
  protected $messageFactory;

  /**
   * The page cache kill switch
   *
   * @var Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch;

  /**
   * Constructor for IdentityProviderController.
   *
   * @param Drupal\Core\Session\AccountInterface $account
   *   Current user account.
   * @param Drupal\saml\Factory\Model\Protocol\SamlMessageFactoryInterface $messageFactory
   *   Saml message factory.
   * @param Drupal\Core\PageCache\ResponsePolicy\KillSwitch $killSwitch
   *   Page cache kill switch.
   */
  public function __construct(
    AccountInterface $account,
    SamlMessageFactoryInterface $messageFactory,
    KillSwitch $killSwitch
  ) {
    $this->account = $account;
    $this->messageFactory = $messageFactory;
    $this->killSwitch = $killSwitch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('saml.response_factory'),
      $container->get('page_cache_kill_switch')
    );
  }

  /**
   * SAML login request.
   *
   * @param Drupal\saml\Entity\ServiceProviderInterface $serviceProvider
   *
   * @return Symfony\Component\HttpFoundation\Response
   *   An instance of Symfony's http response.
   */
  public function login(ServiceProviderInterface $serviceProvider) {
    $this->killSwitch->trigger();

    $message = $this
      ->messageFactory
      ->create($serviceProvider, $this->account);
    $messageContext = (new MessageContext())
      ->setMessage($message);

    return (new BindingFactory())
      ->create(SamlConstants::BINDING_SAML2_HTTP_POST)
      ->send($messageContext);
  }

  public function metadata() {

  }

}
