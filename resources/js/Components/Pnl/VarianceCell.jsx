import { ArrowDownOutlined, ArrowUpOutlined } from '@ant-design/icons';
import { Typography } from 'antd';
import { formatCurrency } from '@/Utils/currency';

export default function VarianceCell({ value, variance }) {
    if (variance == null) {
        return <span>{formatCurrency(value)}</span>;
    }

    const isPositive = variance.delta_percent >= 0;
    const severity = variance.severity ?? 'info';
    const bgColors = {
        info: 'transparent',
        warning: 'rgba(250, 173, 20, 0.15)',
        critical: 'rgba(255, 77, 79, 0.15)',
    };
    const arrowColor = isPositive ? '#3f8600' : '#cf1322';

    return (
        <div style={{ background: bgColors[severity], padding: '2px 4px', borderRadius: 4 }}>
            <div>{formatCurrency(value)}</div>
            <Typography.Text style={{ fontSize: 11, color: arrowColor }}>
                {isPositive ? <ArrowUpOutlined /> : <ArrowDownOutlined />}
                {' '}
                {Math.abs(variance.delta_percent ?? 0).toFixed(1)}%
            </Typography.Text>
        </div>
    );
}
