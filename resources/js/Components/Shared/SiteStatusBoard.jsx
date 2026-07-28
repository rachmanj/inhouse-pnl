import { Link } from '@inertiajs/react';
import { ProCard } from '@ant-design/pro-components';
import { List, Space, Typography } from 'antd';
import StatusChip from '@/Components/Shared/StatusChip';

export default function SiteStatusBoard({ sites = [] }) {
    return (
        <ProCard title="Site Status" bordered>
            <List
                size="small"
                dataSource={sites}
                locale={{ emptyText: 'No site data for this period' }}
                renderItem={(site) => (
                    <List.Item
                        actions={[
                            <StatusChip key="status" status={site.snapshot_status ?? site.period_status ?? 'draft'} />,
                        ]}
                    >
                        <Space direction="vertical" size={0}>
                            <Link href={route('pnl.site.show', site.code)}>
                                <Typography.Text strong>{site.code}</Typography.Text>
                            </Link>
                            <Typography.Text type="secondary" style={{ fontSize: 12 }}>
                                {site.name}
                            </Typography.Text>
                        </Space>
                    </List.Item>
                )}
            />
        </ProCard>
    );
}
