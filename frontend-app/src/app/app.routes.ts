import { Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { RegisterComponent } from './register/register.component';
import { AdminPageComponent } from './adminpage/adminpage.component';
import { ForgotPasswordComponent } from './forgot-password/forgot-password.component';
import { ResetPasswordComponent } from './reset-password/reset-password.component';
import { HomeComponent } from './components/home/home.component';
import { ProductsComponent } from './components/products/products.component';
import { CartComponent } from './components/cart/cart.component';
import { SearchResultsComponent } from './components/search/searchresults.component';
import { DisplayAllComponent } from './components/display-all/display-all.component';

export const routes: Routes = [
  { path: '', redirectTo: '/home', pathMatch: 'full' }, // redirect te /home në root
  { path: 'home', component: HomeComponent },           // rruga e saktë për user
  { path: 'products', component: ProductsComponent },
  { path: 'cart',     component: CartComponent },
  { path: 'display-all', component: DisplayAllComponent },
  { path: 'search',   component: SearchResultsComponent },
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'admin', component: AdminPageComponent },
  { path: 'forgot-password', component: ForgotPasswordComponent },
  { path: 'reset-password', component: ResetPasswordComponent },
  { path: '**', redirectTo: '/home' } // fallback në rast rruge të panjohura
];
