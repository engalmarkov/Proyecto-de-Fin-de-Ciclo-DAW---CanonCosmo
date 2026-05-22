import { Routes } from '@angular/router';
import { HomeComponent } from './components/home/home';
import { ProductListComponent } from './components/product-list/product-list';
import { ProductDetailComponent } from './components/product-detail/product-detail';
import { CartComponent } from './components/cart/cart';
import { LoginComponent } from './components/login/login';
import { RegisterComponent } from './components/register/register';
import { OrderSuccessComponent } from './components/order-success/order-success';
import { ProfileComponent } from './components/profile/profile';
import { Envios } from './envios/envios';
import { adminGuard } from './guards/admin.guard'; 
import { AdminDashboardComponent } from './components/admin/admin-dashboard';
import { AdminProductosComponent } from './components/admin/admin-productos';
import { AdminProductoFormComponent } from './components/admin/admin-producto-form';
import { DevolucionesComponent } from './components/devoluciones/devoluciones';
import { FaqComponent } from './components/faq/faq';
import { ContactoComponent } from './components/contacto/contacto';
import { PedidoDetalleComponent } from './components/pedido-detalle/pedido-detalle';
import { CalendarioJuegosComponent } from './components/calendario-juegos/calendario-juegos';
import { AdminCalendarioComponent } from './components/admin/admin-calendario';

// Importaremos los componentes de admin aquí arriba según los vayamos creando
// import { AdminDashboardComponent } from './components/admin/dashboard/dashboard';

export const routes: Routes = [
  // --- RUTAS PÚBLICAS Y DE USUARIO ---
  { path: '', component: HomeComponent },
  { path: 'productos', component: ProductListComponent },
  { path: 'productos/:id', component: ProductDetailComponent },
  { path: 'pedidos/:id', component: PedidoDetalleComponent },
  { path: 'carrito', component: CartComponent },
  { path: 'login', component: LoginComponent },
  { path: 'registro', component: RegisterComponent },
  { path: 'pedido-exito', component: OrderSuccessComponent },
  { path: 'perfil', component: ProfileComponent },
  { path: 'envios', component: Envios },
  { path: 'devoluciones', component: DevolucionesComponent },
  { path : 'faq', component: FaqComponent },
  { path : 'contacto', component: ContactoComponent },
  { path: 'calendario', component: CalendarioJuegosComponent },
  // --- PANEL DE ADMINISTRACIÓN ---
  { 
    path: 'admin', 
    canActivate: [adminGuard],
    children: [
      // Aquí iremos enganchando las páginas del panel según las creemos:
      
    { path: '', component: AdminDashboardComponent },
    { path: 'productos', component: AdminProductosComponent },
    { path: 'productos/nuevo', component: AdminProductoFormComponent },
    { path: 'productos/editar/:id', component: AdminProductoFormComponent },
    { path: 'calendario', component: AdminCalendarioComponent },
      // { path: 'usuarios', component: AdminUsuariosComponent }, // (/admin/usuarios)
    ]
  },

  { path: '**', redirectTo: '' }
];