import ApplicationLogo from '@/Components/ApplicationLogo';
import { useDarkMode } from '@/Hooks/useDarkMode';
import { Link } from '@inertiajs/react';
import { BulbFilled, BulbOutlined } from '@ant-design/icons';
import { Button, Card, ConfigProvider, theme, Tooltip, Typography } from 'antd';
import enUS from 'antd/locale/en_US';

function AuthShell({ children, isDark, toggleTheme }) {
    const { token } = theme.useToken();

    return (
        <div
            style={{
                minHeight: '100vh',
                background: `linear-gradient(160deg, ${token.colorBgLayout} 0%, ${token.colorBgContainer} 100%)`,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                padding: 24,
                position: 'relative',
            }}
        >
            <Tooltip title={isDark ? 'Switch to light mode' : 'Switch to dark mode'}>
                <Button
                    type="text"
                    icon={isDark ? <BulbFilled /> : <BulbOutlined />}
                    onClick={toggleTheme}
                    style={{
                        position: 'absolute',
                        top: 16,
                        right: 16,
                        zIndex: 10,
                    }}
                />
            </Tooltip>

            <Card
                style={{
                    width: '100%',
                    maxWidth: 420,
                    boxShadow: token.boxShadowSecondary,
                    borderRadius: token.borderRadiusLG,
                }}
            >
                <div style={{ textAlign: 'center', marginBottom: 24 }}>
                    <Link
                        href="/"
                        style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: 12,
                            textDecoration: 'none',
                        }}
                    >
                        <ApplicationLogo
                            style={{ width: 40, height: 40, fill: token.colorPrimary }}
                        />
                        <Typography.Title level={3} style={{ margin: 0, color: token.colorText }}>
                            ArkaLedger
                        </Typography.Title>
                    </Link>
                    <Typography.Text type="secondary" style={{ display: 'block', marginTop: 4 }}>
                        In-House P&L Dashboard
                    </Typography.Text>
                </div>
                {children}
            </Card>
        </div>
    );
}

export default function GuestLayout({ children }) {
    const { isDark, toggleTheme } = useDarkMode(true);

    return (
        <ConfigProvider
            locale={enUS}
            theme={{
                algorithm: isDark ? theme.darkAlgorithm : theme.defaultAlgorithm,
            }}
        >
            <AuthShell isDark={isDark} toggleTheme={toggleTheme}>
                {children}
            </AuthShell>
        </ConfigProvider>
    );
}
