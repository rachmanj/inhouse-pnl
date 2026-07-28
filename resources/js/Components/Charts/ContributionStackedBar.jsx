import { Column } from '@ant-design/charts';

export default function ContributionStackedBar({ data = [], height = 320 }) {
    const config = {
        data,
        xField: 'site',
        yField: 'amount',
        seriesField: 'category',
        isStack: true,
        height,
        legend: { position: 'top' },
        yAxis: {
            label: {
                formatter: (v) => `${(Number(v) / 1e9).toFixed(1)}B`,
            },
        },
        tooltip: {
            formatter: (datum) => ({
                name: datum.category,
                value: new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(datum.amount),
            }),
        },
    };

    return <Column {...config} />;
}
