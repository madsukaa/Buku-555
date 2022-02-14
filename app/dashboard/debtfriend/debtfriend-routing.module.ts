import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { DebtfriendPage } from './debtfriend.page';

const routes: Routes = [
  {
    path: '',
    component: DebtfriendPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class DebtfriendPageRoutingModule {}
