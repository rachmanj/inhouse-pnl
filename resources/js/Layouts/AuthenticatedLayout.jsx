import { Link, router, usePage } from '@inertiajs/react';
import {
    BankOutlined,
    BulbFilled,
    BulbOutlined,
    DashboardOutlined,
    FileTextOutlined,
    ImportOutlined,
    LogoutOutlined,
    SettingOutlined,
    TeamOutlined,
    UserOutlined,
    WalletOutlined,
} from '@ant-design/icons';
import { ProLayout } from '@ant-design/pro-layout';
import { Button, ConfigProvider, Dropdown, message, theme } from 'antd';
import { useEffect } from 'react';
import { useDarkMode } from '@/Hooks/useDarkMode';
import { usePermission } from '@/Hooks/usePermission';

export default function AuthenticatedLayout({ children, title }) {
    const { auth, flash } = usePage().props;
    const { can } = usePermission();
    const { isDark, toggleTheme } = useDarkMode(auth?.user?.dark_mode ?? true);

    useEffect(() => {
        if (flash?.success) {
            message.success(flash.success);
        }
        if (flash?.error) {
            message.error(flash.error);
        }
    }, [flash]);

    const menuItems = [
        can('pnl.view-all-sites') || can('pnl.view-own-site') ? {
            path: '/dashboard',
            name: 'Dashboard',
            icon: <DashboardOutlined />,
        } : null,
        can('imports.manage') || can('imports.create') ? {
            path: '/imports',
            name: 'Import',
            icon: <ImportOutlined />,
        } : null,
        can('reports.generate') ? {
            path: '/reports',
            name: 'Reports',
            icon: <FileTextOutlined />,
        } : null,
        can('tax.manage') ? {
            path: '/tax',
            name: 'Tax',
            icon: <BankOutlined />,
        } : null,
        can('journals.manage') ? {
            path: '/journals',
            name: 'Journals',
            icon: <FileTextOutlined />,
        } : null,
        can('pettycash.manage') ? {
            path: '/petty-cash',
            name: 'Petty Cash',
            icon: <WalletOutlined />,
        } : null,
        can('sites.manage') ? {
            path: '/admin/project-sites',
            name: 'Project Sites',
            icon: <SettingOutlined />,
        } : null,
        can('accounts.manage') ? {
            path: '/admin/accounts',
            name: 'Accounts',
            icon: <SettingOutlined />,
        } : null,
        can('coa-mappings.manage') ? {
            path: '/admin/coa-mappings',
            name: 'CoA Mappings',
            icon: <SettingOutlined />,
        } : null,
        can('users.manage') ? {
            path: '/admin/users',
            name: 'Users',
            icon: <TeamOutlined />,
        } : null,
        can('roles.manage') ? {
            path: '/admin/roles',
            name: 'Roles',
            icon: <TeamOutlined />,
        } : null,
    ].filter(Boolean);

    const userMenu = {
        items: [
            {
                key: 'profile',
                label: <Link href="/profile">Profile</Link>,
                icon: <UserOutlined />,
            },
            {
                key: 'logout',
                label: 'Logout',
                icon: <LogoutOutlined />,
                onClick: () => router.post('/logout'),
            },
        ],
    };

    return (
        <ConfigProvider
            theme={{
                algorithm: isDark ? theme.darkAlgorithm : theme.defaultAlgorithm,
            }}
        >
            <ProLayout
                title="ArkaLedger"
                logo={false}
                layout="mix"
                fixSiderbar
                location={{ pathname: window.location.pathname }}
                route={{ path: '/', routes: menuItems }}
                menuItemRender={(item, dom) => (
                    <Link href={item.path || '/'}>{dom}</Link>
                )}
                actionsRender={() => [
                    <Button
                        key="theme"
                        type="text"
                        icon={isDark ? <BulbFilled /> : <BulbOutlined />}
                        onClick={toggleTheme}
                    />,
                    <Dropdown key="user" menu={userMenu}>
                        <Button type="text" icon={<UserOutlined />}>
                            {auth?.user?.name}
                        </Button>
                    </Dropdown>,
                ]}
            >
                <div style={{ padding: 24 }}>
                    {title && <h2 style={{ marginBottom: 16 }}>{title}</h2>}
                    {children}
                </div>
            </ProLayout>
        </ConfigProvider>
    );
}
