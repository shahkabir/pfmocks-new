(async function () {
  const send = (s) => fetch('/_det_log.php', {method:'POST', body:s});
  const orig = HTMLMediaElement.prototype.play;
  HTMLMediaElement.prototype.play = function () {
    const src = this.src.split('/').pop(); const t0 = performance.now();
    const p = orig.call(this);
    p.then(() => send('resolved ' + src + ' after ' + Math.round(performance.now()-t0) + 'ms'),
           (e) => send('rejected ' + src + ' ' + e.name + ': ' + e.message));
    return p;
  };
  const cards = Array.from(document.querySelectorAll('.det-question'));
  const visible = () => cards.findIndex((c) => c.style.display !== 'none');
  document.querySelector('[data-det-action="begin"]').click();
  while (visible() !== 24) document.querySelector('[data-det-action="next"]').click();
  await new Promise(r => setTimeout(r, 3000));
  const a = cards[24].querySelector('audio');
  send('LSL1 state ready=' + a.readyState + ' net=' + a.networkState + ' paused=' + a.paused + ' err=' + (a.error && a.error.code) + ' label=' + cards[24].querySelector('[data-role="audio-remaining"]').textContent + ' dur=' + a.duration);
  send('DONE');
})();
