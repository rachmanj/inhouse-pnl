import { Head, router } from '@inertiajs/react';
import { Space } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PnlGrid from '@/Components/Pnl/PnlGrid';
import ViewToggle from '@/Components/Pnl/ViewToggle';
import PeriodSelector from '@/Components/Shared/PeriodSelector';

export default function Site({
    site,
    period,
    rows = [],
    view = 'pnl',
    baselineYear = 2024,
    currentYear,
}) {
    const handleViewChange = (newView) => {
        router.get(
            route('pnl.site.show', site.code),
            { period: period?.id, view: newView },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout title={`P&L — ${site.code} ${site.name}`}>
            <Head title={`P&L ${site.code}`} />
            <Space style={{ marginBottom: 16 }} wrap>
                <PeriodSelector />
                <ViewToggle value={view} onChange={handleViewChange} />
            </Space>
            <PnlGrid
                rows={rows}
                baselineYear={baselineYear}
                currentYear={currentYear ?? period?.year}
            />
        </AuthenticatedLayout>
    );
}
