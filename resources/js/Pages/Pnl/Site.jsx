import { Head, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Segmented, Select } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatCurrency } from '@/Utils/currency';

export default function Site({ site, period, view, grid, periods, sites }) {
    const monthCols = Array.from({ length: 12 }, (_, i) => ({
        title: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][i],
        render: (_, r) => formatCurrency(r.current?.months?.[i + 1] ?? 0),
        width: 100,
    }));

    const columns = [
        { title: 'Account', dataIndex: 'name', fixed: 'left', width: 220 },
        { title: `${grid.baseline_year} (baseline)`, children: [...monthCols, { title: 'TOTAL', render: (_, r) => formatCurrency(r.baseline?.total ?? 0) }] },
        { title: `${grid.current_year} (current)`, children: [...monthCols, { title: 'TOTAL', render: (_, r) => formatCurrency(r.current?.total ?? 0) }] },
    ];

    return (
        <AuthenticatedLayout title={`P&L — ${site.code}`}>
            <Head title={`P&L — ${site.code}`} />
            <div style={{ marginBottom: 16, display: 'flex', gap: 16 }}>
                <Select value={period?.id} style={{ width: 140 }} onChange={(v) => router.get(`/pnl/sites/${site.code}`, { period_id: v, view }, { preserveState: true })}
                    options={periods.map((p) => ({ value: p.id, label: `${p.year}-${String(p.month).padStart(2, '0')}` }))} />
                <Select value={site.code} style={{ width: 120 }} onChange={(code) => router.get(`/pnl/sites/${code}`, { period_id: period?.id, view })}
                    options={sites.map((s) => ({ value: s.code, label: s.code }))} />
                <Segmented value={view} onChange={(v) => router.get(`/pnl/sites/${site.code}`, { period_id: period?.id, view: v }, { preserveState: true })}
                    options={[{ label: 'P&L', value: 'pnl' }, { label: 'Rincian', value: 'rincian' }]} />
            </div>
            <ProTable rowKey="id" search={false} options={false} scroll={{ x: 2400 }} columns={columns} dataSource={grid.rows} pagination={false} />
        </AuthenticatedLayout>
    );
}
