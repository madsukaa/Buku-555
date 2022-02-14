import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { AdddebtPage } from './adddebt.page';

const routes: Routes = [
  {
    path: '',
    component: AdddebtPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class AdddebtPageRoutingModule {}
