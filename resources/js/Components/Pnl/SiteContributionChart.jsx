import ContributionStackedBar from '@/Components/Charts/ContributionStackedBar';
import { ProCard } from '@ant-design/pro-components';
import { Checkbox, Space } from 'antd';

export default function SiteContributionChart({
    data = [],
    sites = [],
    excludedSites = [],
    onExcludeChange,
    height = 320,
}) {
    const filteredData = data.filter((row) => !excludedSites.includes(row.site));

    return (
        <ProCard title="Site Contribution" bordered>
            {sites.length > 0 && onExcludeChange && (
                <Space wrap style={{ marginBottom: 16 }}>
                    <span style={{ color: 'rgba(255,255,255,0.45)' }}>Exclude sites:</span>
                    {sites.map((site) => (
                        <Checkbox
                            key={site.code}
                            checked={excludedSites.includes(site.code)}
                            onChange={(e) => onExcludeChange(site.code, e.target.checked)}
                        >
                            {site.code}
                        </Checkbox>
                    ))}
                </Space>
            )}
            <ContributionStackedBar data={filteredData} height={height} />
        </ProCard>
    );
}
