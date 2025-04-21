import { Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { RegisterComponent } from './register/register.component';
import { UserPageComponent } from './userpage/userpage.component';
import { AdminPageComponent } from './adminpage/adminpage.component';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  {path:'register',component:RegisterComponent},
  {path:'user',component:UserPageComponent},
  {path:'admin',component:AdminPageComponent}
];
