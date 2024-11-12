<?php
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;

class PlgSystemCursoreffect extends CMSPlugin
{
    protected $app;

    public function onAfterRender()
    {
        // Sprawdź, czy jesteśmy na froncie (nie na zapleczu)
        if ($this->app->isClient('site')) {
            // Pobierz wygenerowaną treść strony
            $body = $this->app->getBody();

            // Pobierz ścieżkę do skryptu od użytkownika z konfiguracji wtyczki
            $scriptPath = $this->params->get('script_path', 'plugins/system/cursoreffect/js/cursoreffect.js');

            // Jeśli ścieżka nie została ustawiona, zakończ działanie
            if (empty($scriptPath)) {
                return;
            }

            // Pobierz parametry wtyczki
            $async = $this->params->get('async', 1) ? 'async' : '';
            $defer = $this->params->get('defer', 1) ? 'defer' : '';
            $customAttributes = $this->params->get('custom_attributes', '');

            // Przygotuj atrybuty skryptu
            $attributes = trim("$async $defer");

            // Przetwórz dodatkowe atrybuty
            if (!empty($customAttributes)) {
                $lines = explode("\n", trim($customAttributes));
                foreach ($lines as $line) {
                    list($key, $value) = array_map('trim', explode('=', $line, 2));
                    $attributes .= ' ' . $key . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
                }
            }

            // Przygotuj tag <script> z dynamiczną ścieżką
            $scriptTag = '<script src="' . htmlspecialchars($scriptPath, ENT_QUOTES) . '" ' . $attributes . '></script>';

            // Dodaj skrypt zaraz po otwarciu znacznika <body>
            $body = preg_replace('/<body([^>]*)>/i', '<body$1>' . "\n" . $scriptTag, $body, 1);

            // Zaktualizuj wygenerowaną treść strony
            $this->app->setBody($body);
        }
    }
}
