import { createStore, createLogger } from 'vuex'
import register from './modules/register'
import members from './modules/members'

export default createStore({
  modules: {
    register,
    members
  },
})