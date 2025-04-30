import { Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { RegisterComponent } from './register/register.component';
import { UserPageComponent } from './userpage/userpage.component';
import { AdminPageComponent } from './adminpage/adminpage.component';
import { ForgotPasswordComponent } from './forgot-password/forgot-password.component';
import { ResetPasswordComponent } from './reset-password/reset-password.component';
import { HomeComponent }             from './components/home/home.component';
import { ProductsComponent }         from './components/products/products.component';
import { CartComponent } from './components/cart/cart.component';
import { SearchComponent } from './components/search/search.component';

export const routes: Routes = [
  // { path: '', redirectTo: '', pathMatch: 'full' },
  { path: '', component: HomeComponent },
  { path: 'products', component: ProductsComponent },
  { path: 'cart',     component: CartComponent },
  { path: 'search',   component: SearchComponent },
  { path: 'login', component: LoginComponent },
  {path:'register',component:RegisterComponent},
  {path:'user',component:UserPageComponent},
  {path:'admin',component:AdminPageComponent},
  {path:'forgot-password',component:ForgotPasswordComponent},
  {path:'reset-password',component:ResetPasswordComponent}

];
