import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ChatComponent } from './chat/chat.component';
import { ErrorComponent } from './error/error.component';
import { HomeComponent } from './home/home.component';
import { LoginComponent } from './login/login.component';
import { MisProyectosComponent } from './mis-proyectos/mis-proyectos.component';
import { PerfilComponent } from './perfil/perfil.component';
import { RegistroComponent } from './registro/registro.component';
import { VerProyectoComponent } from './ver-proyecto/ver-proyecto.component';

const routes: Routes = [
  {path:'inicio', component:MisProyectosComponent},
  {path:'proyecto/:id', component:VerProyectoComponent},
  {path:'chat/:id', component:ChatComponent},
  {path: 'home', component: HomeComponent},
  {path: 'login', component: LoginComponent},
  {path: 'registro', component: RegistroComponent},
  {path: 'perfil', component: PerfilComponent},
  {path: '', redirectTo: 'login', pathMatch: 'full'},
  //{path:"", redirectTo:"/inicio", pathMatch:"full"},
  {path:"**", component:ErrorComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
