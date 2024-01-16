import {
    mdiMenu,
    mdiClockOutline,
    mdiCloud,
    mdiCrop,
    mdiAccount,
    mdiCogOutline,
    mdiEmail,
    mdiLogout,
    mdiThemeLightDark,
    mdiCloudDownload,
    mdiReact,
    mdiCog,
} from "@mdi/js";

export default [
    /*
    {
      icon: mdiMenu,
      label: "Sample menu",
      menu: [
        {
          icon: mdiClockOutline,
          label: "Item One",
        },
        {
          icon: mdiCloud,
          label: "Item Two",
        },
        {
          isDivider: true,
        },
        {
          icon: mdiCrop,
          label: "Item Last",
        },
      ],
    },*/
    {
        isCurrentUser: true,
        menu: [
            {
                icon: mdiAccount,
                label: "My Profile",
                route: "profile.profile",
                resource:"user",
            },
            {
                isDivider: true,
            },
            {
                icon: mdiLogout,
                label: "Log Out",
                isLogout: true,
            },
        ],
    },
    {
        icon: mdiThemeLightDark,
        label: "Light/Dark",
        isDesktopNoLabel: true,
        isToggleLightDark: true,
    },

    {
        icon: mdiCog,
        resource: 'setting',
        label: "Settings",
        menu: [{
            icon: mdiCloudDownload,
            label: "DB Backup",
            href: "/admin/dbbackup",
            resource: 'dbbackup'
        }, {
            isDivider: true,
        }, {
            icon: mdiCog,
            label: "Public Holidays",
            isDesktopNoLabel: false,
            route: "public-holidays.index",
            resource: 'setting'
        }, {
                icon: mdiCog,
                label: "Settings",
                isDesktopNoLabel: false,
                route: "setting.list",
                resource: 'setting'
            },]
    },

    {
        icon: mdiLogout,
        label: "Log out",
        isDesktopNoLabel: true,
        isLogout: true,
    },
];
