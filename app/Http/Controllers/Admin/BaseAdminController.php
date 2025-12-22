<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseAdminController extends Controller
{
    protected function getAdminLayoutVariables()
    {
        return [
            'siteName' => \App\Helpers\SettingsHelper::siteName(),
            'faviconUrl' => \App\Helpers\SettingsHelper::faviconUrl(),
            'dynamicCss' => \App\Helpers\SettingsHelper::generateDynamicCss(),
            'primaryColor' => \App\Helpers\SettingsHelper::primaryColor(),
        ];
    }

    protected function view($view, $data = [])
    {
        return view($view, array_merge($data, $this->getAdminLayoutVariables()));
    }
}