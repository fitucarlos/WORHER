import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'prioridadFilter'
})
export class PrioridadFilterPipe implements PipeTransform {

  transform(listas: any, valorMuyBaja: boolean, valorBaja: boolean, valorMedia: boolean, valorAlta: boolean, valorMuyAlta: boolean): any[] {

    if (listas && listas.length) {

      let copiaTareas: any[] = [];

      for (let j = 0; j < listas.length; j++) {
        if ((valorMuyBaja && listas[j].prioridad == 1) || (valorBaja && listas[j].prioridad == 2) ||
          (valorMedia && listas[j].prioridad == 3) || (valorAlta && listas[j].prioridad == 4) ||
          (valorMuyAlta && listas[j].prioridad == 5)) {
          copiaTareas.push(listas[j]);
        }

      }
      return listas.filter((tarea: any) => copiaTareas.includes(tarea))



    }

    return listas;
  }

}
