import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-changepass',
  templateUrl: './changepass.page.html',
  styleUrls: ['./changepass.page.scss'],
})
export class ChangepassPage implements OnInit {

  constructor(
    private router: Router
  ) { }

  ngOnInit() {
  }

  dismiss() {
    this.router.navigate(['/dashboard/dashboard/profile']);
  }


}
