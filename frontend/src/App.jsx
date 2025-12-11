import { Fragment } from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import routes from "./routes";

function App() {
  return (
    <Fragment>
      <BrowserRouter>
        <Routes>
          {routes.map((route, idx) => {
            const Component = route.component;
            const Layout = route.layout || Fragment;
            return (
              <Route
                key={idx}
                path={route.path}
                element={<Layout>{Component}</Layout>}
              />
            );
          })}
        </Routes>
      </BrowserRouter>
    </Fragment>
  );
}

export default App;
