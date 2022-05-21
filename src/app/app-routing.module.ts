import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ChatComponent } from './chat/chat.component';
import { ErrorComponent } from './error/error.component';
import { MisProyectosComponent } from './mis-proyectos/mis-proyectos.component';
import { VerProyectoComponent } from './ver-proyecto/ver-proyecto.component';

const routes: Routes = [
  {path:'inicio', component:MisProyectosComponent},
  {path:'proyecto/:id', component:VerProyectoComponent},
  {path:'chat/:id', component:ChatComponent},
  {path:"", redirectTo:"/inicio", pathMatch:"full"},
  {path:"**", component:ErrorComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
