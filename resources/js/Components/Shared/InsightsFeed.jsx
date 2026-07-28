import { AlertOutlined } from '@ant-design/icons';
import { ProCard } from '@ant-design/pro-components';
import { Empty, List, Tag, Typography } from 'antd';

const SEVERITY_COLORS = {
    info: 'blue',
    warning: 'gold',
    critical: 'red',
};

export default function InsightsFeed({ insights = [] }) {
    return (
        <ProCard title="Insights" bordered>
            {insights.length === 0 ? (
                <Empty
                    image={Empty.PRESENTED_IMAGE_SIMPLE}
                    description="No variance flags or anomalies yet"
                />
            ) : (
                <List
                    size="small"
                    dataSource={insights}
                    renderItem={(item) => (
                        <List.Item>
                            <List.Item.Meta
                                avatar={<AlertOutlined style={{ color: SEVERITY_COLORS[item.severity] ?? '#1677ff' }} />}
                                title={(
                                    <SpaceInline>
                                        <Tag color={SEVERITY_COLORS[item.severity] ?? 'default'}>
                                            {item.type ?? item.severity}
                                        </Tag>
                                        <Typography.Text>{item.title}</Typography.Text>
                                    </SpaceInline>
                                )}
                                description={item.description}
                            />
                        </List.Item>
                    )}
                />
            )}
        </ProCard>
    );
}

function SpaceInline({ children }) {
    return <span style={{ display: 'inline-flex', alignItems: 'center', gap: 8 }}>{children}</span>;
}
