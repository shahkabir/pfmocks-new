// Drives the player end to end and prints results into the DOM for --dump-dom.
(async function () {
    const params = new URLSearchParams(location.search);
    const cards = Array.from(document.querySelectorAll('.det-question'));
    const next = () => document.querySelector('[data-det-action="next"]').click();
    const wait = (ms) => new Promise((r) => setTimeout(r, ms));
    const visible = () => cards.findIndex((c) => c.style.display !== 'none');
    const timer = () => document.getElementById('detTimer').textContent;
    const type = (c) => c.querySelector('.badge').textContent.trim();
    const log = [];
    const send = (s) => { log.push(s); fetch('/_det_log.php', {method:'POST', body:s}); }; const check = (name, ok, extra) => send((ok ? 'PASS ' : 'FAIL ') + name + (extra !== undefined ? ' :: ' + extra : ''));

    if (params.has('shot')) {
        document.querySelector('[data-det-action="begin"]').click();
        const target = parseInt(params.get('shot'), 10);
        while (visible() !== target && visible() !== -1) next();
        return;
    }

    window.addEventListener('error', (e) => send('FAIL jsError :: ' + e.message));

    check('63 cards', cards.length === 63, cards.length);
    document.querySelector('[data-det-action="begin"]').click();

    // Read and Complete
    let c = cards[0];
    check('first is Read and Complete', type(c).startsWith('Read and Complete'), type(c));
    const cloze = c.querySelector('.det-cloze');
    const inputs = c.querySelectorAll('[data-role="blank"]');
    check('blanks render', inputs.length >= 6, inputs.length);
    check('no space before punctuation', !/\s[.,]/.test(cloze.textContent), cloze.textContent.slice(0, 80));
    check('prefix shown', cloze.textContent.includes('pract'));
    check('timer 3 min', /^0[23]:/.test(timer()), timer());
    inputs[0].value = 'ice';
    next();
    next(); next(); // remaining C-Tests

    // Read and Select: choosing auto-advances
    let i = visible();
    check('Read and Select word', cards[i].querySelector('.det-word') && type(cards[i]) === 'Read and Select');
    check('5 sec timer', timer() === '00:05' || timer() === '00:04', timer());
    cards[i].querySelector('[value="yes"]').click();
    await wait(450);
    check('choice auto-advances', visible() === i + 1, visible());
    i = visible();
    await wait(5600);
    check('timeout auto-advances', visible() === i + 1, visible());
    next(); // to first Interactive Reading (past the third word)
    i = visible();

    // Interactive Reading: connected, shared timer, no self-answering
    c = cards[i];
    check('IR first step', type(c).includes('Complete the Sentences'), type(c));
    const groupTime = timer();
    check('group timer ~7 min', /^0[67]:/.test(groupTime), groupTime);
    check('partial passage has gap', c.querySelector('[data-role="passage"]').textContent.includes('_____'));
    check('partial hides later text', !c.querySelector('[data-role="passage"]').textContent.includes('However'));
    check('4 options', c.querySelectorAll('[data-role="option"]').length === 4);
    c.querySelector('[data-role="option"]').click();
    await wait(1100);
    next();
    const t2 = timer();
    check('group timer keeps running', t2 < groupTime && /^0[67]:/.test(t2), groupTime + ' -> ' + t2);
    next();
    c = cards[visible()];
    check('highlight step', type(c).includes('Highlight'), type(c));
    const passage = c.querySelector('[data-role="passage"]');
    const full = passage.textContent;
    const answer = 'parks and gardens offer a wide variety of flowers throughout the season';
    const at = full.indexOf(answer);
    const range = document.createRange();
    range.setStart(passage.firstChild, at);
    range.setEnd(passage.firstChild, at + answer.length);
    const sel = getSelection(); sel.removeAllRanges(); sel.addRange(range);
    passage.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));
    check('highlight marked', passage.querySelector('mark') && passage.querySelector('mark').textContent === answer);
    check('highlight offsets', c.highlight && c.highlight.start === at && c.highlight.end === at + answer.length, JSON.stringify(c.highlight && [c.highlight.start, c.highlight.end, at]));
    check('highlight preview', c.querySelector('[data-role="highlight-preview"]').textContent === answer);
    c.querySelector('[data-role="highlight-clear"]').click();
    check('highlight clear', !passage.querySelector('mark') && passage.textContent === full);
    while (!type(cards[visible()]).startsWith('Vocabulary')) next();
    check('VIC timer 30s', /^00:(30|29)/.test(timer()), timer());
    check('VIC input', cards[visible()].querySelectorAll('[data-role="blank"]').length === 1);
    while (!type(cards[visible()]).startsWith('Listen and Select')) next();

    // Listen and Select autoplays with a replay limit
    c = cards[visible()];
    await wait(1500);
    check('LSL has no visible word', !c.querySelector('.det-word'));
    check('LSL autoplay used a play', c.querySelector('[data-role="audio-remaining"]').textContent === 'Plays left: 1', c.querySelector('[data-role="audio-remaining"]').textContent);
    while (!type(cards[visible()]).startsWith('Listen and Type')) next();

    // Dictation: replays
    c = cards[visible()];
    const au = c.querySelector('audio');
    const btn = c.querySelector('[data-role="audio-play"]');
    const left = () => c.querySelector('[data-role="audio-remaining"]').textContent;
    check('LTY 3 plays at start', left() === 'Plays left: 3', left());
    check('LTY timer 60s', /^0[01]:/.test(timer()), timer());
    au.playbackRate = 8;
    for (let p = 0; p < 3; p++) {
        btn.click(); check('disabled immediately ' + p, btn.disabled);
        await wait(300);
        check('button disabled while playing ' + p, btn.disabled);
        await new Promise((r) => { if (au.ended) r(); else au.addEventListener('ended', r, { once: true }); });
        await wait(60);
    }
    check('LTY plays exhausted', left() === 'Plays left: 0' && btn.disabled, left() + ' dur=' + au.duration);
    c.querySelector('[data-role="text-input"]').value = 'typed';
    while (!type(cards[visible()]).includes('Listen and Respond')) next();

    // Interactive Listening
    c = cards[visible()];
    check('IL scenario shown', c.querySelector('.det-group').textContent.includes('library assistant'));
    check('IL shared timer ~4 min', /^0[34]:/.test(timer()), timer());
    next(); next();
    c = cards[visible()];
    check('summary step', type(c).includes('Summarize'), type(c));
    check('summary own timer', /^01:1/.test(timer()), timer());
    const ta = c.querySelector('textarea');
    ta.value = 'one two three'; ta.dispatchEvent(new Event('input'));
    check('word count', c.querySelector('[data-role="word-count"]').textContent === '3');
    while (!type(cards[visible()]).startsWith('Write About')) next();

    // Writing
    c = cards[visible()];
    const img = c.querySelector('img');
    await wait(300);
    check('photo loads', img.complete && img.naturalWidth > 0, img.src);
    while (!type(cards[visible()]).startsWith('Interactive Writing')) next();
    c = cards[visible()];
    const stages = c.querySelectorAll('[data-role="stage"]');
    check('IW part 1 only', stages[0].style.display !== 'none' && stages[1].style.display === 'none');
    check('IW timer 5 min', /^0[45]:/.test(timer()), timer());
    check('IW Continue label', document.getElementById('detNextLabel').textContent === 'Continue');
    const idx = visible();
    stages[0].querySelector('textarea').value = 'first';
    next();
    check('IW same question', visible() === idx);
    check('IW part 2 shown', stages[1].style.display !== 'none');
    check('IW part 1 locked', stages[0].querySelector('textarea').readOnly);
    check('IW timer 3 min', /^0[23]:/.test(timer()), timer());
    check('IW Next label', document.getElementById('detNextLabel').textContent === 'Next');
    next();
    check('IW advances', visible() === idx + 1);
    while (!type(cards[visible()]).startsWith('Speak About')) next();

    // Speaking with a fake microphone
    c = cards[visible()];
    check('SAP 90s', /^01:[23]/.test(timer()), timer());
    const rec = c.querySelector('[data-role="recorder"]');
    rec.querySelector('[data-role="record-start"]').click();
    await wait(1200);
    check('recording', rec.querySelector('[data-role="record-status"]').textContent === 'Recording…', rec.querySelector('[data-role="record-status"]').textContent);
    rec.querySelector('[data-role="record-stop"]').click();
    await wait(1500);
    const pb = rec.querySelector('[data-role="playback"]');
    check('playback ready', pb.src.startsWith('blob:') && pb.style.display !== 'none');
    check('blob non-empty', rec.recording && rec.recording.size > 0, rec.recording && rec.recording.size);
    next();

    // Recording still running when the student moves on
    c = cards[visible()];
    const rec2 = c.querySelector('[data-role="recorder"]');
    rec2.querySelector('[data-role="record-start"]').click();
    await wait(800);
    const m2 = c.dataset.mappingId;
    next();
    await wait(1500);
    check('recording auto-stops on leave', !!rec2.recording);

    while (!type(cards[visible()]).startsWith('Interactive Speaking')) next();
    c = cards[visible()];
    const turns = c.querySelectorAll('[data-role="turn"]');
    check('ISP 6 turns', turns.length === 6, turns.length);
    check('ISP 35s', /^00:3[45]/.test(timer()), timer());
    await wait(300);
    check('ISP prompt autoplay', turns[0].querySelector('[data-role="audio-remaining"]').textContent === 'Plays left: 1', turns[0].querySelector('[data-role="audio-remaining"]').textContent);
    const ii = visible();
    for (let t = 1; t < 6; t++) {
        next();
        check('ISP turn ' + (t + 1) + ' alone', turns[t].style.display !== 'none' && turns[t - 1].style.display === 'none');
        check('ISP timer reset', /^00:3[45]/.test(timer()), timer());
    }
    next();
    check('ISP moves on after last turn', visible() === ii + 1);

    while (visible() !== -1) next();
    await wait(100);
    const f = document.getElementById('detFinish');
    check('finish shown', f.style.display !== 'none');
    check('nav hidden', document.getElementById('detNav').style.display === 'none');
    check('timer cleared', timer() === '--:--', timer());
    check('answered count', parseInt(document.getElementById('detAnsweredCount').textContent, 10) >= 8, document.getElementById('detAnsweredCount').textContent);
    check('answered total', document.getElementById('detAnsweredTotal').textContent === '63');

    const pre = document.createElement('pre'); fetch('/_det_log.php', {method:'POST', body:'DONE'});
    pre.id = 'driverResults';
    pre.textContent = '\n@@@\n' + log.join('\n') + '\n@@@\n';
    document.body.appendChild(pre);
})().catch((e) => {
    const pre = document.createElement('pre'); fetch('/_det_log.php', {method:'POST', body:'DONE'});
    pre.textContent = '\n@@@\nFAIL driver crash :: ' + e.message + '\n' + e.stack + '\n@@@\n';
    document.body.appendChild(pre);
});
