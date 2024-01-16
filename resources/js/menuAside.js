import {

  mdiAccountSupervisor,
  mdiViewDashboard,
  mdiAccountBoxMultiple,
  mdiAccountGroup,
  mdiMonitorEye,
  mdiAccountEye,
  mdiArchiveEye, mdiArrowDecision, mdiAccountMultiplePlus, mdiLayersSearch

} from "@mdi/js";

export default [
  {
    route: "dashboard",
    icon: mdiViewDashboard,
    label: "Dashboard",
  },
  {
    label: "User & Roles",
    icon: mdiAccountSupervisor,
    menu: [
      {
        route: "user.index",
        label: "Users",
        icon: mdiAccountBoxMultiple,
        resource: 'user'
      },
      {
        route: "role.index",
        label: "Role",
        icon: mdiAccountGroup,
        resource: 'role'
      },
    ],
  },
  {
    label: "Logs",
    icon: mdiMonitorEye,
    menu: [
      {
        route: "signinlog.index",
        label: "Signin Logs",
        icon: mdiAccountEye,
        resource: 'signinlog'
      },
      {
        route: "activitylog.index",
        label: "Activity Logs",
        icon: mdiArchiveEye,
        resource: 'activitylog'
      },

    ],
  },
  {
    route: "jobrequest.index",
    icon: mdiArrowDecision,
    label: "New BB Job Requests",
    resource: 'jobrequest'
  },
  {
    route: "bbapplicant.index",
    icon: mdiAccountMultiplePlus,
    label: "New BB Applicants",
    resource: 'bbapplicant'
  },
  {
    route: "jobsearch.index",
    icon: mdiLayersSearch,
    label: "Search MBJ",
    resource: 'jobsearch'
  },
];
