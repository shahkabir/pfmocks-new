{{-- Read and Complete (C-Test) · Vocabulary in Context. Inline inputs replace {n} tokens. Vars: $q, $saved --}}
@php
    $savedBlanks = $saved?->response_blanks ?? [];
    $blanks      = $q->blanks->keyBy('blank_order');
    $parts       = preg_split('/\{(\d+)\}/', (string) $q->question_text, -1, PREG_SPLIT_DELIM_CAPTURE);

    // Built here, not in Blade: template whitespace would show as a space before punctuation in the text.
    $cloze = '';
    foreach ($parts as $i => $part) {
        if ($i % 2 === 0) {
            $cloze .= e($part);
            continue;
        }

        $blank = $blanks->get((int) $part);
        if (!$blank) {
            continue;
        }

        $length = mb_strlen($blank->correct_answer);
        $cloze .= sprintf(
            '<span class="det-blank"><span>%s</span><input type="text" class="det-blank-input" data-role="blank"'
                . ' data-order="%d" style="width:%dch" maxlength="%d" aria-label="Blank %d" value="%s"'
                . ' autocomplete="off" autocapitalize="off" spellcheck="false"></span>',
            e($blank->visible_prefix),
            $blank->blank_order,
            max($length, 3) + 1,
            $length + 10,
            $blank->blank_order,
            e($savedBlanks[(string) $blank->blank_order] ?? ''),
        );
    }
@endphp

<div class="det-cloze">{!! $cloze !!}</div>
