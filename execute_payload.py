
import time
from locust import HttpUser, task, between

class QuickstartUser(HttpUser):
    wait_time = between(1, 5)

    @task
    def index_1(self):

        self.client.get('/ajax/do_your_magic', headers={"content-type":"application/json"} )
