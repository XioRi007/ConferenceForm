import { createStore, createLogger } from 'vuex'
import register from './modules/register'
import members from './modules/members'

// const debug = process.env.NODE_ENV !== 'production'

export default createStore({
  modules: {
    register,
    members
  },
//   strict: debug,
//   plugins: debug ? [createLogger()] : []
})