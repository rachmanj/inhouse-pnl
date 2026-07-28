import { Head, Link } from '@inertiajs/react';
import { Button, Space } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DueDateRadar from '@/Components/Tax/DueDateRadar';
import PeriodSelector from '@/Components/Shared/PeriodSelector';

export default function Calendar({ filings = [] }) {
    return (
        <AuthenticatedLayout title="Tax Calendar">
            <Head title="Tax Calendar" />
            <Space style={{ marginBottom: 16 }}>
                <PeriodSelector />
                <Link href={route('tax.index')}>
                    <Button>Back to Filings</Button>
                </Link>
            </Space>
            <DueDateRadar filings={filings} />
        </AuthenticatedLayout>
    );
}
