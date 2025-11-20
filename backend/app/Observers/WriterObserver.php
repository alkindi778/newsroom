<?php

namespace App\Observers;

use App\Models\Writer;
use App\Jobs\TranslateWriterJob;

class WriterObserver
{
    /**
     * Handle the Writer "created" event.
     */
    public function created(Writer $writer): void
    {
        $this->translateWriter($writer);
    }

    /**
     * Handle the Writer "updated" event.
     */
    public function updated(Writer $writer): void
    {
        if ($writer->wasChanged(['name', 'bio', 'position', 'specialization'])) {
            $this->translateWriter($writer);
        }
    }

    /**
     * ترجمة الكاتب
     */
    private function translateWriter(Writer $writer): void
    {
        TranslateWriterJob::dispatch($writer->id);
    }

    /**
     * Handle the Writer "deleted" event.
     */
    public function deleted(Writer $writer): void
    {
        //
    }

    /**
     * Handle the Writer "restored" event.
     */
    public function restored(Writer $writer): void
    {
        //
    }

    /**
     * Handle the Writer "force deleted" event.
     */
    public function forceDeleted(Writer $writer): void
    {
        //
    }
}
