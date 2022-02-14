import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { DashboardPage } from './dashboard.page';

const routes: Routes = [
  {
    path: 'dashboard',
    component: DashboardPage,
    children: [
      {
        path: 'home',
        children: [
          {
            path: '',
            loadChildren: () => import('../home/home.module').then(m => m.HomePageModule)
          }
        ]
      },
      {
        path: 'debt',
        children: [
          {
            path: '',
            loadChildren: () => import('../debt/debt.module').then(m => m.DebtPageModule)
          }
        ]
      },
      {
        path: 'history',
        children: [
          {
            path: '',
            loadChildren: () => import('../history/history.module').then(m => m.HistoryPageModule)
          }
        ]
      },
      {
        path: 'profile',
        children: [
          {
            path: '',
            loadChildren: () => import('../profile/profile.module').then(m => m.ProfilePageModule)
          }
        ]
      },
      {
        path: 'oweleft',
        children: [
          {
            path: 'debt/oweleft',
            loadChildren: () => import('../oweleft/oweleft.module').then(m => m.OweleftPageModule)
          }
        ]
      },
      {
        path: 'oweright',
        children: [
          {
            path: 'debt/oweright',
            loadChildren: () => import('../oweright/oweright.module').then(m => m.OwerightPageModule)
          }
        ]
      },
      {
        path: 'adddebt',
        children: [
          {
            path: '',
            loadChildren: () => import('../adddebt/adddebt.module').then(m => m.AdddebtPageModule)
          }
        ]
      },
      {
        path: '',
        redirectTo: '/dashboard/home',
        pathMatch: 'full'
      }
    ]
  },
  {
    path: '',
    redirectTo: 'dashboard/home',
    pathMatch: 'full'
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class DashboardPageRoutingModule {}
