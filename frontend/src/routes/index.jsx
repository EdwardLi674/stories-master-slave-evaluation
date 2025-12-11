import UserHome from "../pages/user/Home";
import UserLayout from "../layouts/UserLayout";

export const routes = [
  {
    path: "/",
    layout: UserLayout,
    component: <UserHome />,
  },
];

export default routes;
