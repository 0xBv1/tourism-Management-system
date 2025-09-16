<?php

namespace App\Services\Translation\Google;

use App\Services\Translation\Translator;
use Stichoza\GoogleTranslate\GoogleTranslate;

class FreeTranslator extends Translator
{
    public function translate(): ?string
    {
        if (empty($this->sourceText) ||
            $this->targetLanguage === $this->sourceLanguage ||
            strlen($this->sourceText) > 5000 ||
            str($this->sourceText)->stripTags()->trim()->isEmpty()) {
            return $this->sourceText;
        }

        return GoogleTranslate::trans(
            string: $this->sourceText,
            target: $this->targetLanguage,
            source: $this->sourceLanguage
        );
    }
}
