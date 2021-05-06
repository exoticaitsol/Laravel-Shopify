<?php

namespace Osiset\ShopifyApp\Traits;

use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Osiset\ShopifyApp\Actions\AuthenticateShop;
use Osiset\ShopifyApp\Exceptions\MissingAuthUrlException;
use Osiset\ShopifyApp\Exceptions\SignatureVerificationException;
use function Osiset\ShopifyApp\getShopifyConfig;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;

/**
 * Responsible for authenticating the shop.
 */
trait AuthController
{
    /**
     * Installing/authenticating a shop.
     *
     * @return ViewView|RedirectResponse
     */
    public function authenticate(Request $request, AuthenticateShop $authShop)
    {
        // Get the shop domain
        $shopDomain = ShopDomain::fromNative($request->get('shop'));

        // Run the action
        [$result, $status] = $authShop($request);

        if ($status === null) {
            // Show exception, something is wrong
            throw new SignatureVerificationException('Invalid HMAC verification');
        } elseif ($status === false) {
            if (! $result['url']) {
                throw new MissingAuthUrlException('Missing auth url');
            }

            return View::make(
                'shopify-app::auth.fullpage_redirect',
                [
                    'authUrl'    => $result['url'],
                    'shopDomain' => $shopDomain->toNative(),
                ]
            );
        } else {
            // Go to home route
            return Redirect::route(
                getShopifyConfig('route_names.home'),
                ['shop' => $shopDomain->toNative()]
            );
        }
    }

    /**
     * Get session token for a shop.
     *
     * @return ViewView
     */
    public function token(Request $request)
    {
        return View::make(
            'shopify-app::auth.token',
            [
                'shopDomain' => ShopDomain::fromNative($request->query('shop'))->toNative(),
                'target'     => $request->query('target'),
            ]
        );
    }
}
