import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'prioridadFilter'
})
export class PrioridadFilterPipe implements PipeTransform {

  transform(tareas: any[], prioridad: number): any[] {
    if(tareas && tareas.length && prioridad != 0) return tareas.filter(tarea => tarea.prioridad == prioridad);
    return tareas;
  }

}
