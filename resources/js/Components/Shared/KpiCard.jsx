import { ArrowDownOutlined, ArrowUpOutlined } from '@ant-design/icons';
import { ProCard, Statistic } from '@ant-design/pro-components';
import { Typography } from 'antd';
import { formatCurrency } from '@/Utils/currency';

export default function KpiCard({ title, value, prefix, suffix, changePercent, loading = false }) {
    const isPositive = changePercent >= 0;
    const changeColor = isPositive ? '#3f8600' : '#cf1322';

    return (
        <ProCard loading={loading} bordered>
            <Statistic
                title={title}
                value={value}
                prefix={prefix}
                suffix={suffix}
                formatter={(val) => (typeof val === 'number' ? formatCurrency(val) : val)}
            />
            {changePercent != null && (
                <Typography.Text style={{ color: changeColor, fontSize: 13 }}>
                    {isPositive ? <ArrowUpOutlined /> : <ArrowDownOutlined />}
                    {' '}
                    {Math.abs(changePercent).toFixed(1)}% vs baseline
                </Typography.Text>
            )}
        </ProCard>
    );
}
