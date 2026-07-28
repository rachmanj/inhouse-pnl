import { Head, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Select } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatCurrency } from '@/Utils/currency';

export default function Consolidated({ period, grid, siteContributions, periods }) {
    const columns = [
        { title: 'Account', dataIndex: 'name', fixed: 'left', width: 220 },
        { title: 'TOTAL (current)', render: (_, r) => formatCurrency(r.current?.total ?? 0) },
        { title: 'TOTAL (baseline)', render: (_, r) => formatCurrency(r.baseline?.total ?? 0) },
    ];

    return (
        <AuthenticatedLayout title="Consolidated P&L">
            <Head title="Consolidated P&L" />
            <Select value={period?.id} style={{ width: 140, marginBottom: 16 }} onChange={(v) => router.get('/pnl/consolidated', { period_id: v })}
                options={periods.map((p) => ({ value: p.id, label: `${p.year}-${String(p.month).padStart(2, '0')}` }))} />
            <ProTable rowKey="id" search={false} options={false} scroll={{ x: 800 }} columns={columns} dataSource={grid.rows} pagination={false} />
        </AuthenticatedLayout>
    );
}
