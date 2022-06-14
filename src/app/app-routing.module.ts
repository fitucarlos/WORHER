import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AyudaComponent } from './ayuda/ayuda.component';
import { ErrorComponent } from './error/error.component';
import { LoginComponent } from './login/login.component';
import { MisProyectosComponent } from './mis-proyectos/mis-proyectos.component';
import { RegistroComponent } from './registro/registro.component';
import { VerProyectoComponent } from './ver-proyecto/ver-proyecto.component';

const routes: Routes = [
  { path: 'inicio', component: MisProyectosComponent },
  { path: 'proyecto/:id', component: VerProyectoComponent },
  { path: 'login', component: LoginComponent },
  { path: 'registro', component: RegistroComponent },
  { path: 'ayuda', component: AyudaComponent },
  { path: '', redirectTo: (sessionStorage.getItem('id') ? '/inicio' : '/login'), pathMatch: 'full' },
  { path: "**", component: ErrorComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
