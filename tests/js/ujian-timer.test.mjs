import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

const template = readFileSync(new URL('../../resources/views/siswa/ujian/kerjakan.blade.php', import.meta.url), 'utf8');
const script = template.match(/<script>([\s\S]*?)<\/script>/)[1];

function timerHarness(seconds) {
    let now = 1000000;
    let intervalCallback;
    let autoSubmits = 0;
    let reloads = 0;
    let confirmResult = true;
    let confirmEffect = () => {};
    const documentEvents = {};
    const windowEvents = {};
    const classes = new Set();
    const buttons = [{ disabled: false }, { disabled: false }];
    const answerEvents = {};
    const manualEvents = {};
    const elements = {
        'exam-work': {
            dataset: { examSeconds: String(seconds) },
            querySelectorAll: selector => selector === 'button[type="submit"]'
                ? buttons : [{ addEventListener: (name, callback) => { answerEvents[name] = callback; } }],
        },
        'exam-timer': { classList: { toggle: (name, enabled) => enabled ? classes.add(name) : classes.delete(name) } },
        'exam-countdown': { textContent: '' },
        'exam-timer-message': { textContent: '' },
        'exam-auto-submit-form': { submit: () => { autoSubmits++; } },
        'exam-submit-form': {
            removeAttribute: () => {},
            addEventListener: (name, callback) => { manualEvents[name] = callback; },
        },
    };
    vm.runInNewContext(script, {
        document: {
            getElementById: id => elements[id],
            addEventListener: (name, callback) => { documentEvents[name] = callback; },
        },
        window: {
            addEventListener: (name, callback) => { windowEvents[name] = callback; },
            confirm: () => { confirmEffect(); return confirmResult; },
            location: { reload: () => { reloads++; } },
        },
        Date: { now: () => now },
        setInterval: (callback, delay) => { assert.equal(delay, 1000); intervalCallback = callback; return 1; },
        clearInterval: () => {},
    });
    const dispatchSubmit = handler => {
        let prevented = false;
        handler({ preventDefault: () => { prevented = true; } });
        return prevented;
    };
    return {
        advance: ms => { now += ms; },
        tick: () => intervalCallback(),
        resume: () => documentEvents.visibilitychange(),
        pageshow: persisted => windowEvents.pageshow({ persisted }),
        manual: () => dispatchSubmit(manualEvents.submit),
        answer: () => dispatchSubmit(answerEvents.submit),
        confirm: (result, effect = () => {}) => { confirmResult = result; confirmEffect = effect; },
        get display() { return elements['exam-countdown'].textContent; },
        get submitted() { return autoSubmits; },
        get reloads() { return reloads; },
        classes, buttons,
    };
}

test('formats long durations and updates each second', () => {
    const h = timerHarness(18000);
    assert.equal(h.display, '300:00');
    h.advance(1000); h.tick();
    assert.equal(h.display, '299:59');
});

test('turns red below five minutes, not at exactly five minutes', () => {
    const h = timerHarness(300);
    assert.equal(h.classes.has('bg-red-50'), false);
    h.advance(1000); h.tick();
    assert.equal(h.display, '04:59');
    assert.equal(h.classes.has('bg-red-50'), true);
});

test('catches up elapsed time after a background tab is throttled', () => {
    const h = timerHarness(600);
    h.advance(305000); h.resume();
    assert.equal(h.display, '04:55');
});

test('expired countdown submits exactly once and disables answer buttons', () => {
    const h = timerHarness(2);
    h.advance(3000); h.tick(); h.tick(); h.resume();
    assert.equal(h.display, '00:00');
    assert.equal(h.submitted, 1);
    assert.ok(h.buttons.every(button => button.disabled));
    assert.equal(h.answer(), true);
    assert.equal(h.manual(), true);
});

test('zero seconds at page load submits immediately', () => {
    const h = timerHarness(0);
    h.tick();
    assert.equal(h.submitted, 1);
});

test('cancelling manual confirmation keeps countdown running', () => {
    const h = timerHarness(10);
    h.confirm(false);
    assert.equal(h.manual(), true);
    h.advance(10000); h.tick();
    assert.equal(h.submitted, 1);
});

test('expiry during confirmation bypasses manual submit and posts once', () => {
    const h = timerHarness(10);
    h.confirm(true, () => h.advance(11000));
    assert.equal(h.manual(), true);
    assert.equal(h.submitted, 1);
});

test('accepted manual submit prevents subsequent duplicate submissions', () => {
    const h = timerHarness(10);
    assert.equal(h.manual(), false);
    assert.equal(h.manual(), true);
    h.advance(11000); h.tick();
    assert.equal(h.submitted, 0);
});

test('an answer submitted at expiry yields to automatic collection', () => {
    const h = timerHarness(1);
    h.advance(1000);
    assert.equal(h.answer(), true);
    assert.equal(h.submitted, 1);
});

test('ordinary load does not reload; restored history refreshes server state once', () => {
    const h = timerHarness(10);
    h.pageshow(false); h.tick();
    assert.equal(h.reloads, 0);
    h.pageshow(true);
    assert.equal(h.reloads, 1);
});
