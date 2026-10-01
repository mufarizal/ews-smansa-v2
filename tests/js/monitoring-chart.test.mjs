import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

const pages = [
    'resources/views/guru_bk/monitoring/show_siswa.blade.php',
    'resources/views/wali_kelas/kelas_saya/show_siswa.blade.php',
    'resources/views/siswa/dashboard.blade.php',
];

function runChart(page, mode) {
    const template = readFileSync(new URL(`../../${page}`, import.meta.url), 'utf8');
    const script = template.match(/<script>\s*([\s\S]*?)<\/script>/)[1]
        .replace(/{{ Illuminate\\Support\\Js::from\(\$chartLabels\) }}/g, '["01 Oct 2026", "02 Oct 2026"]')
        .replace(/{{ Illuminate\\Support\\Js::from\(\$chartScores\) }}/g, '[0, 0.75]');
    const nodes = {
        'trend-chart-container': { hidden: true, appendChild() {}, closest() { return {}; } },
        'trend-chart': {},
        'trend-fallback': { hidden: false },
        'trend-data': { open: true },
    };
    let config;
    const context = {
        document: {
            getElementById(id) { return nodes[id]; },
            createElement() { return { remove() {} }; },
        },
        getComputedStyle() {
            return { color: '#26332c', backgroundColor: '#ffffff', borderColor: '#dce2d8', fontFamily: 'Instrument Sans' };
        },
    };
    if (mode !== 'missing') {
        context.Chart = function (canvas, options) {
            if (mode === 'error') throw new Error('Canvas unavailable');
            assert.equal(canvas, nodes['trend-chart']);
            config = options;
        };
    }
    vm.runInNewContext(script, context);
    return { nodes, config };
}

for (const page of pages) {
    test(`${page}: renders a responsive chart using the supplied data`, () => {
        const { nodes, config } = runChart(page, 'ready');
        assert.equal(config.type, 'line');
        assert.equal(config.data.datasets[0].data[0], 0);
        assert.equal(config.data.datasets[0].data[1], 0.75);
        assert.equal(config.options.responsive, true);
        assert.equal(config.options.maintainAspectRatio, false);
        assert.equal(nodes['trend-chart-container'].hidden, false);
        assert.equal(nodes['trend-fallback'].hidden, true);
        assert.equal(nodes['trend-data'].open, false);
    });
    for (const mode of ['missing', 'error']) {
        test(`${page}: retains readable data when Chart.js is ${mode}`, () => {
            const { nodes } = runChart(page, mode);
            assert.equal(nodes['trend-chart-container'].hidden, true);
            assert.equal(nodes['trend-fallback'].hidden, false);
            assert.equal(nodes['trend-data'].open, true);
        });
    }
}
