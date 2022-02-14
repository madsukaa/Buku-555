import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-adddebt',
  templateUrl: './adddebt.page.html',
  styleUrls: ['./adddebt.page.scss'],
})
export class AdddebtPage implements OnInit {

  category: boolean = true;

  constructor(
    private router: Router
  ) { }

  ngOnInit() {
  }

  debtpersonal() {
    this.router.navigate(['/debtpersonal']);
  }

  debtfriend() {
    this.router.navigate(['/debtfriend']);
  }

}
