<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ViewExport implements FromView, WithTitle
{
    protected string $view;
    protected array $data;
    protected ?string $title;

    public function __construct(string $view, array $data = [], ?string $title = null)
    {
        $this->view = $view;
        $this->data = $data;
        $this->title = $title;
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }

    public function title(): string
    {
        $raw = $this->title ?? 'Sheet1';
        // Remove invalid characters: : / \ ? * [ ]
        $sanitized = preg_replace('/[:\/\\\?\*\[\]]+/', ' ', $raw) ?? 'Sheet1';
        // Trim spaces and quotes at ends
        $sanitized = trim($sanitized, "' ");
        if ($sanitized === '') {
            $sanitized = 'Sheet1';
        }
        // Max 31 chars for Excel sheet titles
        if (function_exists('mb_substr')) {
            $sanitized = mb_substr($sanitized, 0, 31);
        } else {
            $sanitized = substr($sanitized, 0, 31);
        }
        return $sanitized;
    }
}

