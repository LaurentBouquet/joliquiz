
# Kubernetes install 

- Connect to Kubernetes Dashboard
- Add 3 services, and copy/paste xxxxx-service.yaml
- Add 3 deployment, and copy/paste xxxxx-deployment.yaml
- Edit nginx service ressource YAML file, and add "type: LoadBalancer" below "clusterIP"
```yaml
  clusterIP: ...
  type: LoadBalancer
```
- Open IP address below "External terminations", with your browser


# Okteto install

- Connect to https://okteto.com/ Dashboard
- Click on "Deploy"
- Click on "Stacks"
- Copy/paste joliquiz.okteto content in "Configuration:" input form
- Click on "Deploy"
