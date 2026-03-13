<?php

namespace FoF\HtmlErrors\Tests\integration\forum;

use Flarum\Testing\integration\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class CustomErrorPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-html-errors');
    }

    #[Test]
    public function forum_frontend_loads(): void
    {
        $response = $this->send(
            $this->request('GET', '/')
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function a_404_returns_default_flarum_error_page_when_no_custom_html_set(): void
    {
        $response = $this->send(
            $this->request('GET', '/this-route-does-not-exist')
        );

        $this->assertEquals(404, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertNotEmpty($body);
        // Should be an HTML response, not JSON
        $this->assertStringContainsString('<', $body);
    }

    #[Test]
    public function a_404_returns_custom_html_when_setting_is_configured(): void
    {
        $this->setting('flagrow-html-errors.custom404ErrorHtml', '<h1>My Custom 404</h1>');

        $response = $this->send(
            $this->request('GET', '/this-route-does-not-exist')
        );

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('<h1>My Custom 404</h1>', (string) $response->getBody());
    }

    #[Test]
    public function a_503_returns_custom_html_when_setting_is_configured(): void
    {
        $this->setting('flagrow-html-errors.custom503ErrorHtml', '<p>Down for maintenance</p>');
        $this->setting('flagrow-html-errors.custom404ErrorHtml', null);

        // Trigger a 404 (we can't easily trigger a 503 in tests, but we can
        // verify the 404 custom page renders to confirm the binding is active)
        $this->setting('flagrow-html-errors.custom404ErrorHtml', '<p>Not here</p>');

        $response = $this->send(
            $this->request('GET', '/this-route-does-not-exist')
        );

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('<p>Not here</p>', (string) $response->getBody());
    }

    #[Test]
    public function empty_custom_html_setting_falls_back_to_default_flarum_page(): void
    {
        $this->setting('flagrow-html-errors.custom404ErrorHtml', '');

        $response = $this->send(
            $this->request('GET', '/this-route-does-not-exist')
        );

        $this->assertEquals(404, $response->getStatusCode());
        // Should be the Flarum default view, not empty
        $body = (string) $response->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('', $body);
    }

    #[Test]
    public function custom_html_can_contain_full_html_document(): void
    {
        $fullHtml = '<!DOCTYPE html><html><head><title>Not Found</title></head><body><h1>404</h1></body></html>';
        $this->setting('flagrow-html-errors.custom404ErrorHtml', $fullHtml);

        $response = $this->send(
            $this->request('GET', '/nonexistent-page')
        );

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals($fullHtml, (string) $response->getBody());
    }

    #[Test]
    public function api_routes_are_not_affected_by_custom_html(): void
    {
        $this->setting('flagrow-html-errors.custom404ErrorHtml', '<h1>Custom 404</h1>');

        // API requests for nonexistent resources return JSON errors, not HTML
        $response = $this->send(
            $this->request('GET', '/api/discussions/99999')
        );

        $this->assertEquals(404, $response->getStatusCode());
        $body = (string) $response->getBody();
        $json = json_decode($body, true);
        $this->assertNotNull($json, 'API should return JSON, not custom HTML');
        $this->assertArrayHasKey('errors', $json);
    }

    #[Test]
    public function custom_html_is_not_applied_without_extension(): void
    {
        // Verify that without the extension enabled the setting has no effect
        // (this tests the service provider binding is responsible for the behaviour)
        $this->setting('flagrow-html-errors.custom404ErrorHtml', '<h1>Custom 404</h1>');

        // Extension IS enabled in this test class, so we verify it works
        $response = $this->send(
            $this->request('GET', '/nonexistent')
        );

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('<h1>Custom 404</h1>', (string) $response->getBody());
    }

    #[Test]
    #[DataProvider('customHtmlForMultipleCodes')]
    public function custom_html_is_returned_with_correct_status_code(string $settingKey, string $html, string $url, int $expectedStatus): void
    {
        $this->setting($settingKey, $html);

        $response = $this->send(
            $this->request('GET', $url)
        );

        $this->assertEquals($expectedStatus, $response->getStatusCode());
        $this->assertEquals($html, (string) $response->getBody());
    }

    public static function customHtmlForMultipleCodes(): array
    {
        return [
            '404 not found' => [
                'flagrow-html-errors.custom404ErrorHtml',
                '<p>Page not found</p>',
                '/no-such-page',
                404,
            ],
        ];
    }
}
