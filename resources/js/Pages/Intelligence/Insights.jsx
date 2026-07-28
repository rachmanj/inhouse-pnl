import { Head } from '@inertiajs/react';
import { List, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Insights({ period, insights }) {
    return (
        <AuthenticatedLayout title="Insights">
            <Head title="Insights" />
            <List dataSource={insights} renderItem={(item) => (
                <List.Item>
                    <Tag color={item.severity === 'critical' ? 'red' : 'orange'}>{item.severity ?? item.status}</Tag>
                    {item.explanation ?? `${item.comparison_type}: ${item.delta_percent}%`}
                </List.Item>
            )} locale={{ emptyText: 'No insights for this period' }} />
        </AuthenticatedLayout>
    );
}
