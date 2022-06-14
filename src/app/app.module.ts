import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { CabeceraComponent } from './cabecera/cabecera.component';
import { FooterComponent } from './footer/footer.component';
import { MisProyectosComponent } from './mis-proyectos/mis-proyectos.component';
import { ProyectoTarjetaComponent } from './proyecto-tarjeta/proyecto-tarjeta.component';
import { ErrorComponent } from './error/error.component';
import { BbddProyectosService } from './bbdd-proyectos.service';
import { CargandoComponent } from './cargando/cargando.component';
import { VerProyectoComponent } from './ver-proyecto/ver-proyecto.component';
import { ChatComponent } from './chat/chat.component';
import { ListaComponent } from './lista/lista.component';
import { TareaComponent } from './tarea/tarea.component';
import { PrioridadFilterPipe } from './prioridad-filter.pipe';
import { DragDropModule } from '@angular/cdk/drag-drop'
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { LoginComponent } from './login/login.component';
import { RegistroComponent } from './registro/registro.component';
import { AyudaComponent } from './ayuda/ayuda.component';


@NgModule({
  declarations: [
    AppComponent,
    CabeceraComponent,
    FooterComponent,
    MisProyectosComponent,
    ProyectoTarjetaComponent,
    ErrorComponent,
    CargandoComponent,
    VerProyectoComponent,
    ChatComponent,
    ListaComponent,
    TareaComponent,
    LoginComponent,
    RegistroComponent,
    PrioridadFilterPipe,
    AyudaComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    DragDropModule,
    FormsModule,
    ReactiveFormsModule,
  ],
  providers: [
    BbddProyectosService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
