<?php

namespace FoF\HtmlErrors\Tests\unit\ErrorHandling;

use Flarum\Foundation\ErrorHandling\HandledError;
use Flarum\Foundation\ErrorHandling\ViewFormatter;
use Flarum\Locale\TranslatorInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use FoF\HtmlErrors\ErrorHandling\CustomViewFormatter;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CustomViewFormatterTest extends TestCase
{
    private SettingsRepositoryInterface $settings;
    private CustomViewFormatter $formatter;
    private ServerRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $view = $this->createMock(ViewFactory::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $this->settings = $this->createMock(SettingsRepositoryInterface::class);

        $renderedView = $this->createMock(\Illuminate\Contracts\View\View::class);
        $renderedView->method('render')->willReturn('<html>default flarum error</html>');
        $renderedView->method('with')->willReturnSelf();

        $view->method('make')->willReturn($renderedView);

        $translator->method('trans')->willReturnArgument(0);

        $this->formatter = new CustomViewFormatter($view, $translator, $this->settings);
        $this->request = new ServerRequest();
    }

    #[Test]
    public function it_extends_view_formatter(): void
    {
        $this->assertInstanceOf(ViewFormatter::class, $this->formatter);
    }

    #[Test]
    public function it_returns_custom_html_when_setting_is_set_for_404(): void
    {
        $customHtml = '<h1>Custom 404 Page</h1>';
        $error = new HandledError(new RuntimeException(), 'not_found', 404);

        $this->settings->method('get')
            ->with('flagrow-html-errors.custom404ErrorHtml')
            ->willReturn($customHtml);

        $response = $this->formatter->format($error, $this->request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals($customHtml, (string) $response->getBody());
    }

    #[Test]
    public function it_returns_custom_html_when_setting_is_set_for_403(): void
    {
        $customHtml = '<h1>Custom 403 Page</h1>';
        $error = new HandledError(new RuntimeException(), 'forbidden', 403);

        $this->settings->method('get')
            ->with('flagrow-html-errors.custom403ErrorHtml')
            ->willReturn($customHtml);

        $response = $this->formatter->format($error, $this->request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals($customHtml, (string) $response->getBody());
    }

    #[Test]
    public function it_returns_custom_html_when_setting_is_set_for_500(): void
    {
        $customHtml = '<h1>Custom 500 Page</h1>';
        $error = new HandledError(new RuntimeException(), 'unknown', 500);

        $this->settings->method('get')
            ->with('flagrow-html-errors.custom500ErrorHtml')
            ->willReturn($customHtml);

        $response = $this->formatter->format($error, $this->request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals($customHtml, (string) $response->getBody());
    }

    #[Test]
    public function it_returns_custom_html_when_setting_is_set_for_503(): void
    {
        $customHtml = '<h1>Custom 503 Page</h1>';
        $error = new HandledError(new RuntimeException(), 'maintenance', 503);

        $this->settings->method('get')
            ->with('flagrow-html-errors.custom503ErrorHtml')
            ->willReturn($customHtml);

        $response = $this->formatter->format($error, $this->request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(503, $response->getStatusCode());
        $this->assertEquals($customHtml, (string) $response->getBody());
    }

    #[Test]
    #[DataProvider('arbitraryStatusCodes')]
    public function it_supports_arbitrary_status_codes(int $statusCode): void
    {
        $customHtml = "<h1>Custom {$statusCode} Page</h1>";
        $error = new HandledError(new RuntimeException(), 'unknown', $statusCode);

        $this->settings->method('get')
            ->with("flagrow-html-errors.custom{$statusCode}ErrorHtml")
            ->willReturn($customHtml);

        $response = $this->formatter->format($error, $this->request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($customHtml, (string) $response->getBody());
    }

    public static function arbitraryStatusCodes(): array
    {
        return [
            'HTTP 400' => [400],
            'HTTP 401' => [401],
            'HTTP 429' => [429],
        ];
    }

    #[Test]
    public function it_falls_back_to_parent_when_setting_is_empty(): void
    {
        $error = new HandledError(new RuntimeException(), 'not_found', 404);

        // Parent ViewFormatter also calls settings->get('forum_title'), so use a callback
        $this->settings->method('get')
            ->willReturnCallback(function (string $key) {
                if ($key === 'flagrow-html-errors.custom404ErrorHtml') {
                    return '';
                }

                return null;
            });

        $response = $this->formatter->format($error, $this->request);

        // Falls back to ViewFormatter which renders the view
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('default flarum error', (string) $response->getBody());
    }

    #[Test]
    public function it_falls_back_to_parent_when_setting_is_null(): void
    {
        $error = new HandledError(new RuntimeException(), 'not_found', 404);

        // Parent ViewFormatter also calls settings->get('forum_title'), so allow any key
        $this->settings->method('get')->willReturn(null);

        $response = $this->formatter->format($error, $this->request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('default flarum error', (string) $response->getBody());
    }

    #[Test]
    public function it_uses_correct_setting_key_format(): void
    {
        $error = new HandledError(new RuntimeException(), 'not_found', 404);

        $customSettingKeyChecked = false;
        $this->settings->method('get')
            ->willReturnCallback(function (string $key) use (&$customSettingKeyChecked) {
                if ($key === 'flagrow-html-errors.custom404ErrorHtml') {
                    $customSettingKeyChecked = true;
                }

                return null;
            });

        $this->formatter->format($error, $this->request);

        $this->assertTrue($customSettingKeyChecked, 'Expected settings key flagrow-html-errors.custom404ErrorHtml to be checked');
    }

    #[Test]
    public function custom_html_preserves_status_code_from_handled_error(): void
    {
        $customHtml = '<p>Gone</p>';
        $error = new HandledError(new RuntimeException(), 'gone', 410);

        $this->settings->method('get')
            ->with('flagrow-html-errors.custom410ErrorHtml')
            ->willReturn($customHtml);

        $response = $this->formatter->format($error, $this->request);

        $this->assertEquals(410, $response->getStatusCode());
    }
}
