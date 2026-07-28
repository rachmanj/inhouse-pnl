import { Line } from '@ant-design/charts';

export default function TrendLine({ data = [], height = 320 }) {
    const config = {
        data,
        xField: 'period',
        yField: 'value',
        seriesField: 'series',
        height,
        smooth: true,
        legend: { position: 'top' },
        yAxis: {
            label: {
                formatter: (v) => `${(Number(v) / 1e9).toFixed(1)}B`,
            },
        },
        tooltip: {
            formatter: (datum) => ({
                name: datum.series,
                value: new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(datum.value),
            }),
        },
    };

    return <Line {...config} />;
}
