import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { DebtPage } from './debt.page';

const routes: Routes = [
  {
    path: 'debt',
    component: DebtPage,
    children: [
      {
        path: 'oweleft',
        children: [
          {
            path: '',
            loadChildren: () => import('../oweleft/oweleft.module').then(m => m.OweleftPageModule)
          }
        ]
      },
      {
        path: 'oweright',
        children: [
          {
            path: '',
            loadChildren: () => import('../oweright/oweright.module').then(m => m.OwerightPageModule)
          }
        ]
      },
      {
        path: '',
        redirectTo: 'debt/oweleft',
        pathMatch: 'full'
      },
    ]
  },
  {
    path: '',
    redirectTo: 'debt/oweleft',
    pathMatch: 'full'
  },
  
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class DebtPageRoutingModule {}
