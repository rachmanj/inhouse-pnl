import { Head, router } from '@inertiajs/react';
import { Space } from 'antd';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PnlGrid from '@/Components/Pnl/PnlGrid';
import SiteContributionChart from '@/Components/Pnl/SiteContributionChart';
import PeriodSelector from '@/Components/Shared/PeriodSelector';

export default function Consolidated({
    period,
    rows = [],
    sites = [],
    contributionData = [],
    baselineYear = 2024,
    currentYear,
    excludedSites: initialExcluded = [],
}) {
    const [excludedSites, setExcludedSites] = useState(initialExcluded);

    const handleExcludeChange = (siteCode, excluded) => {
        const next = excluded
            ? [...excludedSites, siteCode]
            : excludedSites.filter((c) => c !== siteCode);
        setExcludedSites(next);
        router.get(
            route('pnl.consolidated.show'),
            { period: period?.id, exclude_sites: next },
            { preserveState: true, preserveScroll: true, only: ['rows', 'contributionData', 'excludedSites'] },
        );
    };

    return (
        <AuthenticatedLayout title="Consolidated P&L">
            <Head title="Consolidated P&L" />
            <Space style={{ marginBottom: 16 }}>
                <PeriodSelector />
            </Space>

            <div style={{ marginBottom: 24 }}>
                <SiteContributionChart
                    data={contributionData}
                    sites={sites}
                    excludedSites={excludedSites}
                    onExcludeChange={handleExcludeChange}
                />
            </div>

            <PnlGrid
                rows={rows}
                baselineYear={baselineYear}
                currentYear={currentYear ?? period?.year}
            />
        </AuthenticatedLayout>
    );
}
