import { Component, Input, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BbddProyectosService } from '../bbdd-proyectos.service';

@Component({
  selector: 'app-chat',
  templateUrl: './chat.component.html',
  styleUrls: ['./chat.component.css']
})
export class ChatComponent implements OnInit {
  @Input() proyecto:any;

  constructor() { }

  ngOnInit(): void {
      
    
  }

  

}
