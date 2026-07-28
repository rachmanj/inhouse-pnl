import { Head, router } from '@inertiajs/react';
import { ProDescriptions } from '@ant-design/pro-components';
import { Select, Space } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import StagingRowTable from '@/Components/Import/StagingRowTable';
import StatusChip from '@/Components/Shared/StatusChip';

export default function Show({
    batch,
    stagingRows = [],
    accounts = [],
    filters = {},
}) {
    const handleAssignAccount = (rowId, accountId) => {
        router.post(route('imports.resolve-mapping', batch.id), {
            staging_row_id: rowId,
            account_id: accountId,
        }, { preserveScroll: true });
    };

    const handleStatusFilter = (status) => {
        router.get(route('imports.show', batch.id), { mapping_status: status || undefined }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout title={`Import Batch #${batch.id}`}>
            <Head title={`Import #${batch.id}`} />

            <ProDescriptions
                column={3}
                style={{ marginBottom: 16 }}
                dataSource={batch}
                columns={[
                    {
                        title: 'Period',
                        render: () => {
                            const p = batch.report_period ?? batch.period;
                            return p ? `${p.year}-${String(p.month).padStart(2, '0')}` : '—';
                        },
                    },
                    {
                        title: 'Site',
                        render: () => batch.project_site?.code ?? batch.site?.code ?? '—',
                    },
                    { title: 'Source', dataIndex: 'source' },
                    {
                        title: 'Status',
                        dataIndex: 'status',
                        render: (s) => <StatusChip status={s} />,
                    },
                    { title: 'File', dataIndex: 'original_filename' },
                    { title: 'Staged Rows', dataIndex: 'staged_rows' },
                    { title: 'Error Rows', dataIndex: 'error_rows' },
                ]}
            />

            <StagingRowTable
                rows={stagingRows}
                accounts={accounts}
                onAssignAccount={handleAssignAccount}
                toolBarRender={() => [
                    <Space key="filters">
                        <span>Mapping status:</span>
                        <Select
                            allowClear
                            placeholder="All"
                            style={{ width: 140 }}
                            value={filters.mapping_status}
                            onChange={handleStatusFilter}
                            options={[
                                { value: 'unmapped', label: 'Unmapped' },
                                { value: 'mapped', label: 'Mapped' },
                                { value: 'ambiguous', label: 'Ambiguous' },
                                { value: 'error', label: 'Error' },
                            ]}
                        />
                    </Space>,
                ]}
            />
        </AuthenticatedLayout>
    );
}
