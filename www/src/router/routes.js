export default [
  {
    path: "/",
    component: () => import("layouts/default"),
    meta: { requiresAuth: true },
    children: [{ path: "", component: () => import("pages/index") }]
  },
  
  {
    path: "/kullanicilar",
    component: () => import("layouts/default"),
    meta: { requiresAuth: true },
    children: [{ path: "", component: () => import("pages/user") }]
  },
  
  {
    path: "/ayarlar/",
    component: () => import("layouts/default"),
    meta: { requiresAuth: true },
    children: [ 
      { path: "", component: () => import("pages/profil") },
      { path: "profil", component: () => import("pages/profil") }
    ]
  },
  

  {
    path: "/login",
    component: () => import("pages/login")
  },
  {
    path: "/resetPassword",
    component: () => import("pages/resetPassword")
  },
  {
    path: "/logout",
    component: () => import("pages/logout")
  },

  
  {
    path: "*",
    component: () => import("pages/404")
  }
];
