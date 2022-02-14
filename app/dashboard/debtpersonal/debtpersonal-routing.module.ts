import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { DebtpersonalPage } from './debtpersonal.page';

const routes: Routes = [
  {
    path: '',
    component: DebtpersonalPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class DebtpersonalPageRoutingModule {}
